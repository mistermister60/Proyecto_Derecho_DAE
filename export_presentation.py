#!/usr/bin/env python3
"""
Exportador de presentación Marp a múltiples formatos
Requiere: npm install -g @marp-team/marp-cli
"""

import subprocess
import sys
from pathlib import Path

PRESENTATION_FILE = Path(__file__).parent / "PRESENTACION_CONSULTORIO_JURIDICO.md"
OUTPUT_DIR = Path(__file__).parent / "exports"

def check_marp_cli():
    """Verifica si marp-cli está instalado"""
    try:
        result = subprocess.run(["marp", "--version"], capture_output=True, text=True)
        if result.returncode == 0:
            print(f"✅ Marp CLI encontrado: {result.stdout.strip()}")
            return True
    except FileNotFoundError:
        pass
    print("❌ Marp CLI no encontrado. Instala con: npm install -g @marp-team/marp-cli")
    return False

def export_pdf():
    """Exporta a PDF"""
    OUTPUT_DIR.mkdir(exist_ok=True)
    output = OUTPUT_DIR / "Presentacion_Consultorio_Juridico_DAE.pdf"
    cmd = [
        "marp", str(PRESENTATION_FILE),
        "--pdf",
        "--output", str(output),
        "--allow-local-files"
    ]
    print(f"📄 Exportando a PDF: {output}")
    result = subprocess.run(cmd, capture_output=True, text=True)
    if result.returncode == 0:
        print(f"✅ PDF generado: {output}")
    else:
        print(f"❌ Error: {result.stderr}")
    return result.returncode == 0

def export_pptx():
    """Exporta a PowerPoint"""
    OUTPUT_DIR.mkdir(exist_ok=True)
    output = OUTPUT_DIR / "Presentacion_Consultorio_Juridico_DAE.pptx"
    cmd = [
        "marp", str(PRESENTATION_FILE),
        "--pptx",
        "--output", str(output),
        "--allow-local-files"
    ]
    print(f"📊 Exportando a PPTX: {output}")
    result = subprocess.run(cmd, capture_output=True, text=True)
    if result.returncode == 0:
        print(f"✅ PPTX generado: {output}")
    else:
        print(f"❌ Error: {result.stderr}")
    return result.returncode == 0

def export_html():
    """Exporta a HTML standalone"""
    OUTPUT_DIR.mkdir(exist_ok=True)
    output = OUTPUT_DIR / "Presentacion_Consultorio_Juridico_DAE.html"
    cmd = [
        "marp", str(PRESENTATION_FILE),
        "--html",
        "--output", str(output),
        "--allow-local-files"
    ]
    print(f"🌐 Exportando a HTML: {output}")
    result = subprocess.run(cmd, capture_output=True, text=True)
    if result.returncode == 0:
        print(f"✅ HTML generado: {output}")
    else:
        print(f"❌ Error: {result.stderr}")
    return result.returncode == 0

def export_images():
    """Exporta cada slide como imagen PNG"""
    OUTPUT_DIR.mkdir(exist_ok=True)
    output_dir = OUTPUT_DIR / "slides"
    output_dir.mkdir(exist_ok=True)
    cmd = [
        "marp", str(PRESENTATION_FILE),
        "--images", "png",
        "--output", str(output_dir),
        "--allow-local-files"
    ]
    print(f"🖼️ Exportando slides como imágenes: {output_dir}/")
    result = subprocess.run(cmd, capture_output=True, text=True)
    if result.returncode == 0:
        print(f"✅ Imágenes generadas en: {output_dir}/")
    else:
        print(f"❌ Error: {result.stderr}")
    return result.returncode == 0

def main():
    if not PRESENTATION_FILE.exists():
        print(f"❌ Archivo no encontrado: {PRESENTATION_FILE}")
        sys.exit(1)
    
    if not check_marp_cli():
        print("\n💡 Instalación rápida:")
        print("   npm install -g @marp-team/marp-cli")
        sys.exit(1)
    
    print(f"\n📋 Presentación: {PRESENTATION_FILE.name}")
    print(f"📁 Directorio salida: {OUTPUT_DIR}/\n")
    
    # Exportar todos los formatos
    formats = [
        ("PDF", export_pdf),
        ("PPTX", export_pptx),
        ("HTML", export_html),
        ("Imágenes PNG", export_images),
    ]
    
    results = {}
    for name, func in formats:
        print(f"\n{'='*50}")
        results[name] = func()
    
    print(f"\n{'='*50}")
    print("📊 RESUMEN DE EXPORTACIÓN:")
    for name, success in results.items():
        status = "✅ OK" if success else "❌ FALLÓ"
        print(f"   {name}: {status}")
    
    if all(results.values()):
        print(f"\n🎉 ¡Todos los formatos exportados exitosamente en {OUTPUT_DIR}/")
    else:
        print(f"\n⚠️  Algunos formatos fallaron. Revisa los errores arriba.")
        sys.exit(1)

if __name__ == "__main__":
    main()