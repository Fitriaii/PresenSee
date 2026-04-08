import os
import time
import json
import shutil
import pickle
import numpy as np
import cv2
import requests

from fastapi import APIRouter
from pydantic import BaseModel
from typing import List, Optional

from utils.insight_engine import face_app
from utils.image_utils import decode_image

router = APIRouter()

BASE_DIR = "dataset"
EMBEDDING_DIR = os.path.join(BASE_DIR, "embeddings")
META_DIR = os.path.join(BASE_DIR, "meta")
IMAGE_DIR = os.path.join(BASE_DIR, "images")

os.makedirs(EMBEDDING_DIR, exist_ok=True)
os.makedirs(META_DIR, exist_ok=True)
os.makedirs(IMAGE_DIR, exist_ok=True)


# ========================== #
# Schema
# ========================== #
class TrainRequest(BaseModel):
    nis: str
    name: str
    kelas_id: str
    images: Optional[List[str]] = []
    urls: Optional[List[str]] = []


# ========================== #
# Endpoint REGISTER
# ========================== #
@router.post("/face-register")
def register_face(payload: TrainRequest):

    nis = payload.nis
    name = payload.name
    kelas_id = str(payload.kelas_id)

    if not nis or not kelas_id:
        return {"success": False, "message": "NIS dan kelas_id wajib"}

    all_images = []

    # Base64
    for base64_img in payload.images:
        img = decode_image(base64_img)
        if img is not None:
            all_images.append(img)

    # URL
    for url in payload.urls:
        try:
            res = requests.get(url, timeout=5)
            if res.status_code == 200:
                img_array = np.frombuffer(res.content, np.uint8)
                img = cv2.imdecode(img_array, cv2.IMREAD_COLOR)
                if img is not None:
                    all_images.append(img)
        except:
            pass

    if not all_images:
        return {"success": False, "message": "Tidak ada gambar"}

    # ========================== #
    # Dataset folder
    # ========================== #
    user_folder = os.path.join(IMAGE_DIR, kelas_id, nis)

    if os.path.exists(user_folder):
        shutil.rmtree(user_folder)

    os.makedirs(user_folder, exist_ok=True)

    embeddings = []
    labels = []
    saved_count = 0

    # ========================== #
    # Proses wajah
    # ========================== #
    for idx, img in enumerate(all_images):

        faces = face_app.get(img)
        if not faces:
            continue

        for i, face in enumerate(faces):

            if face.det_score < 0.6:
                continue

            emb = face.embedding
            emb = emb / np.linalg.norm(emb)

            # crop wajah
            x1, y1, x2, y2 = map(int, face.bbox)
            face_img = img[y1:y2, x1:x2]

            filename = f"{int(time.time()*1000)}_{idx}_{i}.jpg"
            cv2.imwrite(os.path.join(user_folder, filename), face_img)

            embeddings.append(emb)
            labels.append(nis)
            saved_count += 1

    if saved_count == 0:
        return {"success": False, "message": "Tidak ada wajah valid"}

    # ========================== #
    # Simpan embedding per kelas
    # ========================== #
    emb_path = os.path.join(EMBEDDING_DIR, f"{kelas_id}.pkl")

    db = {"encodings": [], "labels": []}

    if os.path.exists(emb_path):
        with open(emb_path, "rb") as f:
            db = pickle.load(f)

    # hapus lama
    db["encodings"] = [
        e for i, e in enumerate(db["encodings"])
        if db["labels"][i] != nis
    ]
    db["labels"] = [l for l in db["labels"] if l != nis]

    # tambah baru
    db["encodings"].extend(embeddings)
    db["labels"].extend(labels)

    with open(emb_path, "wb") as f:
        pickle.dump(db, f)

    # ========================== #
    # Simpan META
    # ========================== #
    meta = {
        "nis": nis,
        "name": name,
        "kelas_id": kelas_id,
        "total_images": saved_count,
        "updated_at": int(time.time())
    }

    with open(os.path.join(META_DIR, f"{nis}.json"), "w") as f:
        json.dump(meta, f, indent=2)

    return {
        "success": True,
        "message": f"{saved_count} wajah berhasil diregister",
        "nis": nis,
        "kelas_id": kelas_id
    }
