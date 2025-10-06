FROM python:3.10-slim
WORKDIR /app
RUN pip install flask
COPY signal_server.py .
EXPOSE 9000
CMD ["python", "signal_server.py"]
