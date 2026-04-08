import base64
import cv2
import numpy as np

def decode_image(base64_str):
    img_bytes = base64.b64decode(base64_str.split(",")[-1])
    img_array = np.frombuffer(img_bytes, np.uint8)
    return cv2.imdecode(img_array, cv2.IMREAD_COLOR)
