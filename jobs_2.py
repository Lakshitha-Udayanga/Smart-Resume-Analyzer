from fastapi import FastAPI
from pydantic import BaseModel
from typing import List
import pandas as pd
import re

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

app = FastAPI()

class Job(BaseModel):
    id: int
    title: str
    company_name: str
    skills: str
    experience_level: str
    education_certificate: str = ""
    link: str = ""


class RecommendationRequest(BaseModel):
    user_profile: str
    jobs: List[Job]



def clean_text(text):
    if not text:
        return ""
    text = text.lower()
    text = re.sub(r'[^a-z0-9\s]', ' ', text)
    return text

SYNONYMS = {
    "py": "python",
    "js": "javascript",
    "reactjs": "react",
    "nodejs": "node",
    "laravel": "php",
    "mysql": "sql",
}

def normalize_words(text):
    words = clean_text(text).split()
    return set([SYNONYMS.get(w, w) for w in words])


def extract_years(text):
    text = text.lower()

    # 3+ years / 3 years
    match = re.search(r'(\d+)\s*\+?\s*(year|yr)', text)
    if match:
        return int(match.group(1))

    # 2-5 years
    match_range = re.search(r'(\d+)\s*-\s*(\d+)\s*(year|yr)', text)
    if match_range:
        low = int(match_range.group(1))
        high = int(match_range.group(2))
        return (low + high) // 2

    # keywords
    if "senior" in text:
        return 5
    elif "mid" in text:
        return 3
    elif "junior" in text:
        return 1
    elif "intern" in text:
        return 0

    return 2


def experience_score(job_exp, user_exp_text):
    job_years = extract_years(job_exp)
    user_years = extract_years(user_exp_text)

    diff = user_years - job_years

    if diff == 0:
        return 1.0
    elif diff >= 2:
        return 0.85
    elif diff >= 0:
        return 0.95
    elif diff >= -1:
        return 0.8
    elif diff >= -3:
        return 0.5
    else:
        return 0.2

@app.post("/match-jobs")
def match_jobs(data: RecommendationRequest):
    try:
        jobs_df = pd.DataFrame([job.dict() for job in data.jobs])

        jobs_df['job_info'] = (
            jobs_df['title'] + " " +
            jobs_df['skills'] + " " +
            jobs_df['experience_level'] + " " +
            jobs_df['education_certificate']
        )

        jobs_df['job_info'] = jobs_df['job_info'].apply(clean_text)

        user_profile = clean_text(data.user_profile)

        tfidf = TfidfVectorizer(stop_words='english', ngram_range=(1, 2))

        all_text = pd.concat([
            pd.Series([user_profile]),
            jobs_df['job_info']
        ])

        tfidf_matrix = tfidf.fit_transform(all_text)
        cosine_sim = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:])

        jobs_df['tfidf_score'] = cosine_sim[0]

        user_keywords = normalize_words(user_profile)

        def skill_score(job_skills):
            job_keywords = normalize_words(job_skills)

            if not job_keywords:
                return 0

            matched = user_keywords.intersection(job_keywords)

            return len(matched) / (len(job_keywords) + 1)

        jobs_df['skill_score'] = jobs_df['skills'].apply(skill_score)

        jobs_df['exp_score'] = jobs_df['experience_level'].apply(
            lambda x: experience_score(x, user_profile)
        )

        jobs_df['final_score'] = (
            jobs_df['tfidf_score'] * 0.6 +
            jobs_df['skill_score'] * 0.25 +
            jobs_df['exp_score'] * 0.15
        )

        jobs_df['final_score'] = (jobs_df['final_score'] * 100).round(2)


        def match_reason(job_skills):
            job_keywords = normalize_words(job_skills)
            matched = user_keywords.intersection(job_keywords)
            return list(matched)[:5]

        jobs_df['matched_skills'] = jobs_df['skills'].apply(match_reason)

        top_jobs = jobs_df.sort_values(
            by='final_score', ascending=False
        ).head(3)

        return top_jobs[
            [
                'id',
                'title',
                'company_name',
                'final_score',
                'matched_skills',
                'link'
            ]
        ].to_dict(orient='records')

    except Exception as e:
        return {"error": str(e)}
