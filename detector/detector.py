import cv2, os, time, requests, base64, json
from ultralytics import YOLO
from datetime import datetime

# CONFIG - change these
RTSP_URL = "rtsp://username:password@camera_ip:554/stream"
PHP_API_URL = "http://php-api:8000/api_receive_detection.php"  # docker network name
CAMERA_ID = "camera_1"
SNAPSHOT_DIR = "../php-api/public/snapshots"  # detector running in container; map volume accordingly
MODEL_PATH = "models/ambulance_yolov8n.pt"  # trained model

os.makedirs(SNAPSHOT_DIR, exist_ok=True)

model = YOLO(MODEL_PATH)  # load model

def post_detection(payload, image_path):
    # multipart post with image
    files = {'image': open(image_path, 'rb')}
    try:
        r = requests.post(PHP_API_URL, data=payload, files=files, timeout=10)
        print("Posted to API:", r.status_code, r.text)
    except Exception as e:
        print("API post failed:", e)

def main():
    cap = cv2.VideoCapture(RTSP_URL)
    if not cap.isOpened():
        print("Cannot open stream, try local file")
        return

    while True:
        ret, frame = cap.read()
        if not ret:
            time.sleep(1)
            continue

        results = model.predict(source=frame, imgsz=640, conf=0.4, save=False, verbose=False)
        # results is a list; get first
        for r in results:
            boxes = r.boxes
            for box in boxes:
                cls = int(box.cls.cpu().numpy()[0])
                conf = float(box.conf.cpu().numpy()[0])
                # Assuming class 0 is 'ambulance' in your dataset. Update accordingly.
                # You may want to train model with only ambulance vs not.
                if cls == 0 and conf > 0.5:
                    x1,y1,x2,y2 = map(int, box.xyxy.cpu().numpy()[0])
                    # save snapshot
                    ts = datetime.utcnow().strftime("%Y%m%d%H%M%S")
                    fname = f"{CAMERA_ID}_{ts}.jpg"
                    path = os.path.join(SNAPSHOT_DIR, fname)
                    cv2.imwrite(path, frame)

                    # optional geo coordinates (if camera has known lat/lng)
                    payload = {
                        'camera_id': CAMERA_ID,
                        'timestamp': datetime.utcnow().strftime("%Y-%m-%d %H:%M:%S"),
                        'confidence': str(conf),
                        'bbox': f"{x1},{y1},{x2},{y2}",
                        'lat': '',  # fill if known
                        'lng': ''
                    }
                    print("Detection:", payload)
                    post_detection(payload, path)

        # small sleep to limit CPU
        time.sleep(0.05)

if __name__ == "__main__":
    main()
