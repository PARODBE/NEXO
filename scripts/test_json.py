"""Pruebas sin escritura de la API JSON del catalogo."""
import json, os, urllib.request, urllib.error
url=os.environ.get('NEXO_TEST_URL','http://localhost/Nexo/').rstrip('/')+'/api/catalogue.php'
cases=[('categoria',b'{"category":"analisis"}',200,'application/json','POST'),('json_roto',b'{',400,'application/json','POST'),('array',b'[]',400,'application/json','POST'),('filtro_desconocido',b'{"unexpected":true}',422,'application/json','POST'),('tipo_incorrecto',b'{"q":[]}',422,'application/json','POST'),('demasiado_grande',b' '*4097,413,'application/json','POST'),('formulario',b'q=test',415,'application/x-www-form-urlencoded','POST'),('metodo',b'{}',405,'application/json','PUT')]
for label,body,expected,content_type,method in cases:
    req=urllib.request.Request(url,data=body,headers={'Content-Type':content_type},method=method)
    try:
        with urllib.request.urlopen(req,timeout=10) as response:
            status=response.status; payload=json.load(response)
    except urllib.error.HTTPError as error:
        status=error.code; payload=json.load(error)
    assert status==expected,(label,status,expected)
    if label=='categoria':
        assert payload['agents'] and all(agent['category']=='analisis' for agent in payload['agents'])
    print(label,status,'OK')
