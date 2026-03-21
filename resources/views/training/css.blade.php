    <style>
        .training-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .training-card:hover {
            transform: translateY(-5px);
        }

        .btn-training {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            color: white !important;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
        }

        .btn-training:hover {
            opacity: 0.9;
            box-shadow: 0 5px 15px rgba(37, 117, 252, 0.4);
        }

        .loader-container {
            display: none;
        }

        .processing .loader-container {
            display: block;
        }

        .processing .btn-text {
            display: none;
        }
    </style>
