# 🚑 Ambulance Detection and Smart Alert System 🚦  

![Ambulance Traffic](https://upload.wikimedia.org/wikipedia/commons/9/9a/Ambulance_stuck_in_traffic.jpg)  
*AI + IoT solution to clear the way for emergency vehicles*  

---

## 📌 Project Overview  

In modern cities, **ambulances often get trapped in heavy traffic**, delaying life-saving medical help ⏳.  
This project introduces an **AI-powered Smart Alert System** that detects ambulances in real-time and automatically manages traffic signals to clear their path.  

✔️ **YOLO + CNN models** detect ambulances from live CCTV or video feeds  
✔️ **PHP + MySQL backend** logs detections & alerts authorities  
✔️ **Traffic signal automation** ensures priority passage  
✔️ **Dashboard** for real-time monitoring 🚨  

---

## 🌆 The Real Problem  

![Ambulance Stuck](https://upload.wikimedia.org/wikipedia/commons/4/4c/Ambulance_in_traffic_jam.jpg)  

> Ambulances lose **critical golden minutes** because of congested roads and unaware drivers.  
Our system reduces response time by **instantly detecting ambulances** and controlling **traffic lights automatically**.  

---

## 🛠️ Technology Stack  

| Layer | Technology |
|-------|------------|
| **AI Detection** | YOLOv8, CNN, OpenCV |
| **Backend** | PHP, MySQL |
| **Dashboard** | PHP + JavaScript (Map.js) |
| **Signal Control** | Python (Flask / Socket) |
| **Deployment** | Docker & Docker Compose |

---

---

## 🚀 How It Works  

1. **Detection (YOLOv8)**  
   - CCTV or live camera feed is processed  
   - Ambulance is detected in real-time  

2. **Backend Logging (PHP + MySQL)**  
   - Detection event is stored in DB  
   - Alerts are triggered to nearby authorities  

3. **Smart Traffic Control (Python)**  
   - Traffic signals switch to **green** for the ambulance route  
   - Other lanes turn **red** temporarily  

4. **Dashboard Monitoring**  
   - Police & traffic operators can track events live  
   - Alerts visible on the map 🗺️  

![Workflow](https://miro.medium.com/v2/resize:fit:1200/1*CQ93Qk-kNvztNFVQVw1GcQ.png)  

---

## 🔧 Installation  

### 1️⃣ Clone Repository  
```bash
git clone https://github.com/yourusername/ambulance-alert-system.git
cd ambulance-alert-system



## 📂 System Architecture  

