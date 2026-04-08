from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from routes.face_register import router as register_router
from routes.face_clear import router as clear_router
from routes.face_recognize import router as recognize_router
# from routes.check import router as check_router

def create_app():
    app = FastAPI(
        title="Face Recognition Presensi API",
        version="1.0.0"
    )

    app.add_middleware(
        CORSMiddleware,
        allow_origins=["*"],  # development bebas dulu
        allow_credentials=True,
        allow_methods=["*"],
        allow_headers=["*"],
    )

    app.include_router(register_router, prefix="/api", tags=["Register"])
    app.include_router(clear_router, prefix="/api", tags=["Clear"])
    app.include_router(recognize_router, prefix="/api", tags=["Recognize"])
    # app.include_router(check_router, prefix="/api", tags=['Check'])

    return app

app = create_app()

# RUN:
# uvicorn app:app --host 0.0.0.0 --port 5000
