#!/usr/bin/env python3

import socket

HOST = 'gishosting.gter.it'  # The server's hostname or IP address
PORT = 15201        # The port used by the server

with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
    s.connect((HOST, PORT))
    s.sendall(b'Hello, world')
    data = s.recv(1024)

print('Received', repr(data))
