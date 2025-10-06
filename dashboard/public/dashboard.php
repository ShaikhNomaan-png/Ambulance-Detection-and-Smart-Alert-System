<?php
// dashboard.php - simple list + map
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Ambulance Alerts Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <style>#map{height:420px}</style>
</head>
<body class="p-3">
  <div class="container">
    <h2>Ambulance Alerts Dashboard</h2>
    <div class="row">
      <div class="col-md-6">
        <h5>Recent Detections</h5>
        <div id="detectionsList">Loading...</div>
      </div>
      <div class="col-md-6">
        <h5>Map (Intersections)</h5>
        <div id="map"></div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    const map = L.map('map').setView([20.5937,78.9629], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // load intersections
    fetch('/php-api/get_intersections.php').then(r=>r.json()).then(data=>{
      data.forEach(i=>{
        if(i.lat && i.lng){
          L.marker([i.lat, i.lng]).addTo(map).bindPopup(i.name);
        }
      });
    });

    // load detections (via php api DB query page below)
    async function loadDetections(){
      // This endpoint isn't provided in php-api by default; we'll fetch via a small PHP helper below.
      const res = await fetch('/php-api/get_detections.php');
      const dets = await res.json();
      const c = document.getElementById('detectionsList');
      if(dets.length === 0) {
        c.innerHTML = '<p>No detections yet.</p>';
        return;
      }
      let html = '<table class="table table-sm"><thead><tr><th>#</th><th>Time</th><th>Camera</th><th>Conf</th><th>Image</th></tr></thead><tbody>';
      dets.forEach(d=>{
        html += `<tr>
                  <td>${d.id}</td>
                  <td>${d.timestamp}</td>
                  <td>${d.camera_id}</td>
                  <td>${parseFloat(d.confidence).toFixed(2)}</td>
                  <td>${d.image_path ? '<a href=\"/php-api/'+d.image_path+'\" target=\"_blank\">view</a>' : ''}</td>
                 </tr>`;
      });
      html += '</tbody></table>';
      c.innerHTML = html;
    }

    loadDetections();
    // refresh every 10s
    setInterval(loadDetections, 10000);
  </script>
</body>
</html>
