import os
import pickle
import numpy as np

from fastapi import APIRouter
from pydantic import BaseModel

from utils.insight_engine import face_app
from utils.image_utils import decode_image

router = APIRouter()

EMBEDDING_DIR = "dataset/embeddings"


# ========================== #
# Schema
# ========================== #
class RecognizeRequest(BaseModel):
    image: str
    kelas_id: str


# ========================== #
# Cosine similarity
# ========================== #
def cosine_similarity(a, b):
    return np.dot(a, b)


# ========================== #
# Endpoint
# ========================== #
@router.post("/face_recognize")
def recognize_face(payload: RecognizeRequest):

    image_b64 = payload.image
    kelas_id = str(payload.kelas_id)

    if not image_b64:
        return {"status": "error", "message": "Gambar tidak ditemukan"}

    if not kelas_id:
        return {"status": "error", "message": "ID kelas wajib disertakan"}

    # ---------------------------- #
    # Decode image
    # ---------------------------- #
    try:
        img = decode_image(image_b64)
    except Exception as e:
        return {"status": "error", "message": f"Gagal decode gambar: {str(e)}"}

    # ---------------------------- #
    # Detect face (InsightFace)
    # ---------------------------- #
    faces = face_app.get(img)

    if not faces:
        return {"status": "face_not_found", "message": "Wajah tidak terdeteksi"}

    if len(faces) > 1:
        return {"status": "multiple_faces", "message": "Lebih dari satu wajah"}

    face = faces[0]

    if face.det_score < 0.6:
        return {"status": "low_quality", "message": "Wajah kurang jelas"}

    # ---------------------------- #
    # Embedding
    # ---------------------------- #
    input_enc = face.embedding
    input_enc = input_enc / np.linalg.norm(input_enc)

    x1, y1, x2, y2 = map(int, face.bbox)

    THRESHOLD = 0.45

    matched_nis = None
    matched_class = None
    closest_distance = 1.0

    # ---------------------------- #
    # Loop semua kelas (SAMA seperti Flask)
    # ---------------------------- #
    for file in os.listdir(EMBEDDING_DIR):

        if not file.endswith(".pkl"):
            continue

        kelas_file_id = os.path.splitext(file)[0]
        filepath = os.path.join(EMBEDDING_DIR, file)

        try:
            with open(filepath, "rb") as f:
                db = pickle.load(f)
                encodings = db.get("encodings", [])
                labels = db.get("labels", [])
        except Exception as e:
            print(f"[ERROR] Gagal baca {file}: {e}")
            continue

        if not encodings:
            continue

        # ---------------------------- #
        # Hitung similarity
        # ---------------------------- #
        similarities = np.dot(encodings, input_enc)
        idx = np.argmax(similarities)
        similarity = similarities[idx]

        distance = 1 - similarity

        if distance < THRESHOLD and distance < closest_distance:
            closest_distance = distance
            matched_nis = labels[idx]
            matched_class = kelas_file_id

    # ---------------------------- #
    # Result
    # ---------------------------- #
    if matched_nis:

        confidence = (1.0 - closest_distance) * 100

        if matched_class == kelas_id:
            return {
                "status": "success",
                "siswa_nis": matched_nis,
                "confidence": round(confidence, 2),
                "face_location": {
                    "x": int(x1),
                    "y": int(y1),
                    "width": int(x2 - x1),
                    "height": int(y2 - y1)
                }
            }

        else:
            return {
                "status": "wrong_class",
                "message": f"Wajah dikenali sebagai NIS {matched_nis}, namun tidak terdaftar dalam kelas ini",
                "siswa_nis": matched_nis,
                "kelas_id": matched_class,
                "confidence": round(confidence, 2)
            }

    else:
        return {
            "status": "not_recognized",
            "message": "Wajah tidak dikenali di seluruh kelas."
        }
