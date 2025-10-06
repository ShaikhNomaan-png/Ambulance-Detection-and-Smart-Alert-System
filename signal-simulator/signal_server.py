from flask import Flask, request, jsonify
import logging
app = Flask(__name__)
logging.basicConfig(level=logging.INFO)

@app.route("/", methods=["POST"])
def control_root():
    data = request.get_json(silent=True) or {}
    app.logger.info("Received control command: %s", data)
    # Simulate action success
    return jsonify({"ok": True, "received": data}), 200

# health check
@app.route("/healthz", methods=["GET"])
def health():
    return jsonify({"status":"ok"}), 200

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=9000)
