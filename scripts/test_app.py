"""Pruebas locales del catálogo B2B. Solo crea y elimina sus propias cuentas y solicitudes."""
from pathlib import Path
import urllib.request, urllib.parse, urllib.error, http.cookiejar
import re, json, uuid, subprocess, os

ROOT=Path(__file__).resolve().parent.parent
BASE=os.environ.get('NEXO_TEST_URL','http://localhost/'+urllib.parse.quote(ROOT.name)+'/')
MYSQL=os.environ.get('NEXO_TEST_MYSQL','C:/xampp/mysql/bin/mysql.exe')
PORT=os.environ.get('NEXO_TEST_DB_PORT','3307')
USER=os.environ.get('NEXO_TEST_DB_USER','root')
PASSWORD='PruebaEquipo739Z'
class Client:
    def __init__(self):
        self.client=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
        self.page=''
        self.url=''
    def request(self,path,data=None,status=200):
        body=urllib.parse.urlencode(data,doseq=True).encode() if data is not None else None
        try:
            response=self.client.open(BASE+path,body)
            code=response.status;self.url=response.url;self.page=response.read().decode('utf-8')
        except urllib.error.HTTPError as error:
            code=error.code;self.url=error.url;self.page=error.read().decode('utf-8')
        assert code==status,(path,code,status,self.page[:180])
        assert 'Fatal error' not in self.page and '<b>Warning</b>' not in self.page
        return self.page
    def csrf(self):
        token=re.search(r'name="csrf_token" value="([a-f0-9]+)"',self.page)
        assert token,'Falta token CSRF en la página actual.'
        return token.group(1)
    def post(self,data,status=200):
        return self.request('actions.php',dict(data,csrf_token=self.csrf()),status)
def sql(query):
    result=subprocess.run([MYSQL,'--host=127.0.0.1','--port='+PORT,'--user='+USER,'--database=nexo_business','--batch','--skip-column-names','--execute='+query],capture_output=True,check=True,text=True)
    return result.stdout.strip()

guest=Client();a=Client();b=Client();emails=[];results=[]
try:
    guest.request('');assert 'NEXO' in guest.page.upper()
    catalogue=json.loads(guest.request('api/catalogue.php'))['agents']
    assert len(catalogue)>=6
    ids=[int(agent['id']) for agent in catalogue]
    filtered=json.loads(guest.request('api/catalogue.php?category=analisis'))['agents']
    assert filtered and all(agent['category']=='analisis' for agent in filtered)
    assert not json.loads(guest.request('api/catalogue.php?q=zzznexo-sin-coincidencia'))['agents']
    guest.request('api/catalogue.php?q[]=bad',status=422)
    guest.request('api/catalogue.php?category=bad',status=422)
    guest.request('api/catalogue.php',{},status=415)
    guest.request('actions.php',status=405)
    guest.request('actions.php',{'action':'configuration_delete'},status=403)
    guest.request('agent.php?id='+str(ids[0]))
    guest.request('agent.php?id=999999',status=404)
    results.append('Catálogo, búsqueda, categorías, fichas, vacíos y errores HTTP/CSRF: correctos.')
    for client in [a,b]:
        email='nexo-b2b-'+uuid.uuid4().hex+'@example.invalid';emails.append(email)
        if client is a:
            client.request('builder.php?plan=basico')
        client.request('register.php')
        client.request('register.php',{'csrf_token':client.csrf(),'name':'Empresa de prueba','email':email,'password':PASSWORD})
        assert 'logout.php' in client.page
        if client is a:
            assert urllib.parse.urlparse(client.url).path.endswith('/builder.php')
            assert urllib.parse.parse_qs(urllib.parse.urlparse(client.url).query).get('plan')==['basico']
    owner_id=int(sql("SELECT id FROM nexo_business.users WHERE email='"+emails[0]+"';"))
    name='Equipo prueba '+uuid.uuid4().hex
    ordered=[ids[2],ids[0],ids[1]]
    config={'action':'configuration_save','configuration_id':'','name':name,'objective':'Organizar la preparación de una campaña comercial con revisión humana.','sector':'consultoria','plan':'basico','agents[]':ordered}
    a.post(config)
    config_id=int(sql("SELECT id FROM nexo_business.configurations WHERE user_id="+str(owner_id)+" AND name='"+name+"';"))
    assert sql('SELECT GROUP_CONCAT(agent_id ORDER BY position) FROM nexo_business.configuration_agents WHERE config_id='+str(config_id)+';')==','.join(map(str,ordered))
    b.request('builder.php?id='+str(config_id),status=404)
    b.request('dashboard.php')
    b.post({'action':'configuration_delete','configuration_id':config_id,'confirmation':'ELIMINAR'},status=403)
    results.append('Dos cuentas, plan elegido conservado tras registro, guardado real, orden persistente y permisos de configuración: correctos.')
    a.request('builder.php?id='+str(config_id))
    a.post(dict(config,configuration_id=config_id,objective='Objetivo revisado <script>alert(1)</script> y limitado a un flujo concreto.',**{'agents[]':[ids[1],ids[2]]}))
    a.request('builder.php?id='+str(config_id))
    assert '&lt;script&gt;' in a.page and '<script>alert(1)</script>' not in a.page
    assert sql('SELECT GROUP_CONCAT(agent_id ORDER BY position) FROM nexo_business.configuration_agents WHERE config_id='+str(config_id)+';')==f'{ids[1]},{ids[2]}'
    a.post(dict(config,configuration_id=config_id,**{'agents[]':ids[:4]}))
    assert sql('SELECT COUNT(*) FROM nexo_business.configuration_agents WHERE config_id='+str(config_id)+';')=='2'
    a.request('builder.php?id='+str(config_id))
    a.post(dict(config,configuration_id=config_id,plan='business',**{'agents[]':ids}))
    assert sql('SELECT COUNT(*) FROM nexo_business.configuration_agents WHERE config_id='+str(config_id)+';')==str(len(ids))
    a.request('builder.php?id='+str(config_id))
    a.post(dict(config,configuration_id=config_id,plan='business',**{'agents[]':[ids[0],ids[0]]}))
    assert sql('SELECT COUNT(*) FROM nexo_business.configuration_agents WHERE config_id='+str(config_id)+';')==str(len(ids))
    results.append('Edición, escape XSS, orden nuevo, límite Básico y selección duplicada rechazada: correctos.')
    a.request('request.php?configuration='+str(config_id)+'&plan=business')
    request={'action':'request_create','configuration_id':config_id,'company':'Empresa ficticia de pruebas','contact_email':emails[0],'sector':'consultoria','objective':'Organizar las actividades del equipo seleccionado para una propuesta inicial.','plan':'business','message':'Solicitud ficticia generada por las pruebas locales; no enviar comunicaciones.'}
    a.post(request)
    request_id=int(sql('SELECT MAX(id) FROM nexo_business.requests WHERE user_id='+str(owner_id)+';'))
    assert request_id>0
    a.request('dashboard.php');assert 'Empresa ficticia de pruebas' in a.page
    b.request('dashboard.php');assert 'Empresa ficticia de pruebas' not in b.page
    b.request('request.php?configuration='+str(config_id),status=404)
    a.request('dashboard.php')
    a.request('logout.php',{'csrf_token':a.csrf()})
    a.request('login.php');a.request('login.php',{'csrf_token':a.csrf(),'email':emails[0],'password':PASSWORD})
    a.request('dashboard.php');assert 'Empresa ficticia de pruebas' in a.page
    a.post({'action':'configuration_delete','configuration_id':config_id,'confirmation':'ELIMINAR'})
    a.request('builder.php?id='+str(config_id),status=404)
    assert sql('SELECT COUNT(*) FROM nexo_business.configuration_agents WHERE config_id='+str(config_id)+';')=='0'
    assert sql('SELECT configuration_id IS NULL FROM nexo_business.requests WHERE id='+str(request_id)+';')=='1'
    results.append('Solicitud persistente y privada, sesión, eliminación y conservación de solicitud sin configuración: correctos.')
finally:
    if emails:
        quoted=','.join("'"+email+"'" for email in emails)
        # Solicitudes no se borran en cascada: retirar solo las asociadas a las cuentas aleatorias.
        sql('DELETE r FROM nexo_business.requests r JOIN nexo_business.users u ON u.id=r.user_id WHERE u.email IN ('+quoted+'); DELETE FROM nexo_business.users WHERE email IN ('+quoted+');')

report='# Verificación de NEXO\n\n'+'\n'.join('- '+result for result in results)+'\n\nLas pruebas HTTP utilizan dos cuentas aleatorias y limpian únicamente sus configuraciones y solicitudes. No se envían correos, no se procesan pagos y el flujo de producto es ilustrativo.\n\nLas pruebas de JavaScript se ejecutan con `node scripts/check_interactions.cjs`: comprueban orden y movimiento de agentes, selección sin duplicados, cambio de límite de plan, bloqueo de guardado y cambio del flujo ilustrativo sin peticiones de red. La sintaxis se comprueba con `php -l` y `node --check`. No se ha completado una revisión visual automática en navegador; estas comprobaciones no la sustituyen.\n'
(ROOT/'docs'/'verificacion.md').write_text(report,encoding='utf-8')
print('\n'.join(results))
