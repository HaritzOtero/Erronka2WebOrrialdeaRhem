#!/usr/bin/env python
# coding: utf-8

# In[1]:


# Esta celda conecta a la base de datos MySQL y recupera los usuarios que no tienen una foto de perfil.
# Identifica a los usuarios cuya columna `irudia` está vacía o contiene un valor genérico como `''`.


# In[2]:


import mysql.connector

# Conexión a la base de datos
db = mysql.connector.connect(
    host="localhost",    # Cambia según tu configuración
    user="root",   # Usuario de la base de datos
    password="jS*5i9Q9Z9ox/_4lLVik'*z2KJNt<(1ZPo9Sr#ZiGJMW/1Br4yeJ%`bBzl5<'S+&",  # Contraseña de la base de datos
    database="rhem"     # Nombre de la base de datos
)

cursor = db.cursor()

# Query para obtener usuarios sin foto
query = "SELECT username, izena, abizena FROM users WHERE irudia = '' OR irudia IS NULL"
cursor.execute(query)

# Recuperar los resultados
usuarios_sin_foto = cursor.fetchall()

# Mostrar resultados
if usuarios_sin_foto:
    print("Argazki gabeko usuarioak:")
    for usuario in usuarios_sin_foto:
        print(f"Username: {usuario[0]}, Izena: {usuario[1]}, Abizena: {usuario[2]}")
else:
    print("Usuario guztiak dute argazkia.")

# Cerrar conexión
cursor.close()
db.close()


# In[3]:


# Esta celda envía un correo electrónico a cada usuario que no tiene una foto de perfil subida.
# El correo informa al usuario que debe subir una foto a su perfil.


# In[4]:


import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# Configuración del servidor de correo (smtp4dev en el puerto 5000)
smtp_server = "localhost"  # Cambiado a localhost ya que smtp4dev suele ejecutarse en tu máquina local
smtp_port = 2525          # Puerto 5000 como mencionaste
email_user = "ugarteburutorlojuak@mail.com"  # Cambia esto por tu correo (aunque puede no ser necesario con smtp4dev)

# Función para enviar el correo
def enviar_correo(destinatario, nombre):
    asunto = "Argazkia gehitu!"
    cuerpo = f"""
    Kaixo {nombre},

    Zure kontuan ez argazkia ez daukazula igota konturatu gara.
    Momentu bat daukazunean agrazka igotzea eskatzen dizugu, mesedez.

    Ekerrikasko zure parte-hartzeagatik!

    Informatika taldea.
    """

    # Crear el mensaje
    msg = MIMEMultipart()
    msg["From"] = email_user
    msg["To"] = destinatario
    msg["Subject"] = asunto
    msg.attach(MIMEText(cuerpo, "plain"))

    # Enviar el mensaje
    try:
        with smtplib.SMTP(smtp_server, smtp_port) as server:
            server.sendmail(email_user, destinatario, msg.as_string())
        print(f"Maila bidalita {destinatario} -ri")
    except Exception as e:
        print(f"Errorea korreoa bidaltzen {destinatario}: {e}")

# Enviar correos a los usuarios sin foto
for usuario in usuarios_sin_foto:
    username, nombre, _ = usuario
    enviar_correo(username, nombre)


# In[ ]:




