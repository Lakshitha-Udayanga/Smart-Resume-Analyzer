from fastapi import FastAPI
from pydantic import BaseModel
from typing import List
import pandas as pd
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

def extract_keywords(text):
    if not text:
        return set()
    return set(text.lower().split())

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

        tfidf = TfidfVectorizer(stop_words='english')
        all_text = pd.concat([
            pd.Series([data.user_profile]),
            jobs_df['job_info']
        ])

        tfidf_matrix = tfidf.fit_transform(all_text)
        cosine_sim = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:])
        jobs_df['tfidf_score'] = cosine_sim[0]

        user_keywords = extract_keywords(data.user_profile)

        def skill_score(job_skills):
            job_keywords = extract_keywords(job_skills)
            if len(job_keywords) == 0:
                return 0
            match = user_keywords.intersection(job_keywords)
            return len(match) / len(job_keywords)

        def education_score(job_edu):
            job_edu_keywords = extract_keywords(job_edu)
            if len(job_edu_keywords) == 0:
                return 0.5
            match = user_keywords.intersection(job_edu_keywords)
            return len(match) / len(job_edu_keywords)

        def experience_score(exp):
            exp = exp.lower()
            if "senior" in exp: return 0.9
            elif "mid" in exp: return 0.7
            elif "junior" in exp: return 0.5
            return 0.6

        jobs_df['skill_score'] = jobs_df['skills'].apply(skill_score)
        jobs_df['edu_score'] = jobs_df['education_certificate'].apply(education_score)
        jobs_df['exp_score'] = jobs_df['experience_level'].apply(experience_score)

        jobs_df['final_score'] = (
            jobs_df['tfidf_score'] * 0.4 +
            jobs_df['skill_score'] * 0.4 +
            jobs_df['edu_score'] * 0.2 +
            jobs_df['exp_score'] * 0.2
        )

        jobs_df['final_score'] = (jobs_df['final_score'] * 100).round(2)

        def match_reason(job_skills):
            job_keywords = extract_keywords(job_skills)
            matched = user_keywords.intersection(job_keywords)
            return list(matched)[:5]

        jobs_df['matched_skills'] = jobs_df['skills'].apply(match_reason)

        top_jobs = jobs_df.sort_values(by='final_score', ascending=False).head(3)

        return top_jobs[
            ['id', 'title', 'company_name', 'final_score', 'matched_skills', 'link']
        ].to_dict(orient='records')

    except Exception as e:
        return {"error": str(e)}
