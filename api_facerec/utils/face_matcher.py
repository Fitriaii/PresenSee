import numpy as np
from sklearn.metrics.pairwise import cosine_similarity

THRESHOLD = 0.45  # production-safe

def match_face(embedding, known_embeddings):
    sims = cosine_similarity([embedding], known_embeddings)[0]
    best_score = np.max(sims)
    best_idx = np.argmax(sims)

    if best_score >= THRESHOLD:
        return True, best_idx, float(best_score)
    return False, None, float(best_score)
