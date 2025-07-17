from fastapi import FastAPI, File, UploadFile, Form
from fastapi.staticfiles import StaticFiles
from fastapi.templating import Jinja2Templates
from fastapi.requests import Request
from fastapi.responses import HTMLResponse
from proses import proses

app = FastAPI(title="Klasifikasi Penyakit Kanker")

app.mount("/static", StaticFiles(directory="static"), name="static")
app.mount("/model_gambar", StaticFiles(directory="model_gambar"), name="images")
templates = Jinja2Templates(directory="templates")

@app.get("/", response_class=HTMLResponse)
async def welcome(request: Request):
    return templates.TemplateResponse("index.html", {"request": request})

@app.post("/prediksi")
async def predict_image(skin: UploadFile = File(...)):
    conf, label = proses(skin)
    if conf is None:
        return {"error": label}
    return {"label": label, "confidence": f"{conf * 100:.2f}%"}

@app.get("/ui")
async def main_page(request: Request):
    return templates.TemplateResponse("index.html", {"request": request})