#!/usr/bin/env python3

# first of all import the socket library
import socket
import sys,os
import pynmea2
from datetime import datetime

#now = datetime.datetime.now()
#today = '{}-{}-{}'.format(now.year, now.month, now.day)
# next create a socket object
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
print("Socket successfully created")

# reserve a port on your computer in our
# case it is 12345 but it can be anything
port = 15201

# Next bind to the port
# we have not typed any ip in the ip field
# instead we have inputted an empty string
# this makes the server listen to requests
# coming from other computers on the network
s.bind(('', port))
print("socket binded to %s" %(port))

# put the socket into listening mode
s.listen(5)
print("socket is listening")

# a forever loop until we interrupt it or
# an error occurs
while True:
    # Establish connection with client.
    c, addr = s.accept()
    print('Got connection from', addr)
    #now=datetime.utcnow()
    #ora = '%02d-%02d-%02d %02d:%02d:%02d'%(now.year, now.month, now.day, now.hour, now.minute, now.second)
    data = c.recv(1024)
    #dati=data.split()
    #print(dati)
    #latitude(deg) longitude(deg)  height(m)   Q  ns   sdn(m)   sde(m)   sdu(m)  sdne(m)  sdeu(m)  sdun(m) age(s)  ratio
#    print('Data:{}'.format(dati[0].decode('utf-8')))
    try:
        dati=data.split()
        #print(dati
        msg=pynmea2.parse(dati[0].decode('utf-8'))
        now=datetime.utcnow()
        ora = '%02d-%02d-%02d %02d:%02d:%02d'%(now.year, now.month, now.day, now.hour, now.minute, now.second)
        print('ora UTC {}'.format(ora))
        print('latitudine {}'.format(msg.latitude))
        print('longitudine {}'.format(msg.longitude))
        print('altitude {}'.format(msg.altitude))
        print('qualita fissaggio {}'.format(msg.gps_qual))


    except:
        print('messaggio da ref station')
    #print(type(dati[0].decode('utf-8')))
    #print('Ora:{}'.format(dati[1].decode('utf-8')))
    #print('Lat:{}'.format(float(dati[2])))
    #print('Lon:{}'.format(float(dati[3])))
    #print('quota:{}'.format(float(dati[4])))
    #print('Quality:{}'.format(dati[5]))
    #print('N sat:{}'.format(dati[6]))
    #print('sdn[m]:{}'.format(dati[7]))
    #print('sde[m]:{}'.format(dati[8]))
    #print('sdu[m]:{}'.format(dati[9]))
    #print('sdne[m]:{}'.format(dati[10]))
    #print('sdeu[m]:{}'.format(dati[11]))
    #print('sdun[m]:{}'.format(dati[12]))
    #print('age[s]:{}'.format(dati[13]))
    #print('ratio:{}'.format(dati[14]))
    #print('Received', repr(data))
    # send a thank you message to the client.
    c.send(b'Thank you for connecting') # da python 3 bisogna scrivere in byte, ecco il b davanti

