from insightface.app import FaceAnalysis
import traceback

print(">> Initializing InsightFace...")

try:
    face_app = FaceAnalysis(
        name="buffalo_l",
        providers=["CPUExecutionProvider"]  # PAKSA CPU
    )

    face_app.prepare(
        ctx_id=0,
        det_size=(640, 640)
    )

    print("✅ InsightFace initialized successfully")

except Exception:
    print("❌ FAILED TO INIT INSIGHTFACE")
    traceback.print_exc()
    face_app = None
