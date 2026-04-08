# models/insightface_model.py

from insightface.app import FaceAnalysis
import os


class InsightFaceModel:
    """
    Singleton InsightFace Model Loader
    Load sekali, pakai berkali-kali
    """

    _app = None

    @classmethod
    def get(cls):
        if cls._app is None:
            cls._app = cls._load_model()
        return cls._app

    @staticmethod
    def _load_model():
        """
        Inisialisasi InsightFace FaceAnalysis
        """
        # AUTO GPU / CPU
        ctx_id = 0 if os.getenv("USE_GPU", "1") == "1" else -1

        app = FaceAnalysis(
            name="buffalo_l",  # terbaik untuk recognition
            providers=[
                "CUDAExecutionProvider",
                "CPUExecutionProvider"
            ]
        )

        app.prepare(
            ctx_id=ctx_id,
            det_size=(640, 640)
        )

        print("✅ InsightFace model loaded")
        print(f"   → Provider: {'GPU' if ctx_id == 0 else 'CPU'}")

        return app
