FROM python:3.10-slim

WORKDIR /app

# system deps for OpenCV (minimal)
RUN apt-get update && apt-get install -y --no-install-recommends \
    ffmpeg \
    build-essential \
    libgl1 \
    && rm -rf /var/lib/apt/lists/*

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# copy app
COPY detector.py .
COPY utils/ ./utils
# models volume will be mounted from host (./detector/models)

# create snapshots dir
RUN mkdir -p /app/snapshots

CMD ["python", "detector.py"]
