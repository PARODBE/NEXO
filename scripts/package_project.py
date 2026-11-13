"""Empaqueta la entrega académica, sin sesiones, credenciales ni salida recursiva."""
from pathlib import Path
import zipfile

ROOT = Path(__file__).resolve().parent.parent
OUTPUT = ROOT / 'output'
OUTPUT.mkdir(exist_ok=True)
destination = OUTPUT / 'PabloRodriguezBelenguer_ProyectoFinal_WebApp.zip'
allowed_root = ['index.php','agent.php','builder.php','request.php','dashboard.php','privacy.php','actions.php','login.php','register.php','logout.php','README.md','documentacion_proyecto.md','.gitignore']
allowed_dirs = ['api','assets','classes','config','database','docs','includes','scripts']
blocked_parts = {'__pycache__','node_modules','.git','tmp','output'}
with zipfile.ZipFile(destination,'w',zipfile.ZIP_DEFLATED) as archive:
    for name in allowed_root:
        path = ROOT / name
        if path.is_file():
            archive.write(path, Path('Nexo') / name)
    for name in allowed_dirs:
        for path in sorted((ROOT / name).rglob('*')):
            relative = path.relative_to(ROOT)
            if not path.is_file() or blocked_parts.intersection(relative.parts):
                continue
            if relative.as_posix() == 'config/local.php':
                continue
            if path.name.startswith('.env') or path.name.startswith('sess_') or path.suffix in {'.log','.pyc'}:
                continue
            archive.write(path, Path('Nexo') / relative)
with zipfile.ZipFile(destination) as archive:
    assert archive.testzip() is None
    assert 'Nexo/database/nexo.sql' in archive.namelist()
    assert all('/output/' not in name and 'sess_' not in name and '/.env' not in name for name in archive.namelist())
    print(f'ZIP verificado: {len(archive.namelist())} archivos, {destination.stat().st_size} bytes.')
