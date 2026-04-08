import os
import pickle
import shutil

from fastapi import APIRouter
from pydantic import BaseModel

router = APIRouter()


# ========================== #
# Schema
# ========================== #
class ClearRequest(BaseModel):
    kelas_id: str
    nis: str


# ========================== #
# Endpoint
# ========================== #
@router.post("/face-clear")
def clear_siswa_data(payload: ClearRequest):

    kelas_id = str(payload.kelas_id)
    nis = str(payload.nis)

    if not kelas_id or not nis:
        return {
            "success": False,
            "message": "kelas dan nis wajib dikirim"
        }

    dataset_path = os.path.join("dataset", "images", kelas_id, nis)
    embedding_file = os.path.join("dataset", "embeddings", f"{kelas_id}.pkl")

    errors = []

    # ========================== #
    # Hapus folder wajah siswa
    # ========================== #
    if os.path.exists(dataset_path):
        try:
            shutil.rmtree(dataset_path)
        except Exception as e:
            errors.append(f"Gagal hapus folder dataset siswa: {e}")

    # ========================== #
    # Hapus embedding siswa
    # ========================== #
    if os.path.exists(embedding_file):
        try:
            with open(embedding_file, "rb") as f:
                db = pickle.load(f)

            db["encodings"] = [
                e for i, e in enumerate(db["encodings"])
                if db["labels"][i] != nis
            ]

            db["labels"] = [
                l for l in db["labels"]
                if l != nis
            ]

            with open(embedding_file, "wb") as f:
                pickle.dump(db, f)

        except Exception as e:
            errors.append(f"Gagal hapus embedding siswa: {e}")

    # ========================== #
    # Response
    # ========================== #
    if errors:
        return {
            "success": False,
            "message": "Sebagian data gagal dihapus",
            "errors": errors
        }

    return {
        "success": True,
        "message": f"Data siswa {nis} berhasil dihapus dari kelas dengan ID : {kelas_id}"
    }
