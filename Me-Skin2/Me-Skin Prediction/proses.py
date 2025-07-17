import numpy as np
from tensorflow.keras.preprocessing import image
from tensorflow.keras.models import load_model
import io
from PIL import Image

def proses(uploaded_file):
    try:
        model = load_model('model_imk(final).h5')

        contents = uploaded_file.file.read()
        img = Image.open(io.BytesIO(contents)).convert("RGB")
        img = img.resize((150, 150))

        img_array = image.img_to_array(img)
        img_array = np.expand_dims(img_array, axis=0) / 255.0

        predictions = model.predict(img_array)
        confidence = float(predictions[0][0])
        predicted_class = "Cancer" if confidence > 0.5 else "Non-Cancer"
        return confidence, predicted_class

    except Exception as e:
        return None, f"Error: {str(e)}"
