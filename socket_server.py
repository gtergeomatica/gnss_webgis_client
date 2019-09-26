#!/usr/bin/env python3

# first of all import the socket library
#import socket
#import sys,os

import sys
import socket
import select
import time
from threading import Thread
from binascii import hexlify
import fcntl
import struct
import psycopg2


#file credenziali.py contain the following line
#conn = psycopg2.connect(dbname='name', port=5432, user='username', password='pwd', host='XXX.XXX.XXX.XXX')
import credenziali
conn=credenziali.conn

curr = conn.cursor()
conn.autocommit = True
'''
This class will contain the following methods:

    Constructor: Initialize the socket with a host and port.
    close() : Terminate and wait on each SocketServerThread , and close the main socket.
    run() : Start the socket server, and run until the thread is stopped. For each incoming socket connection, start a new SocketServerThread .
    stop() : Stop the execution of the run()  loop.
'''


class SocketServer(Thread):
    def __init__(self, host = '0.0.0.0', port = 8081, max_clients = 5):
        """ Initialize the server with a host and port to listen to.
        Provide a list of functions that will be used when receiving specific data """
        Thread.__init__(self)
        self.sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        #self.sock = socket.socket(socket.AF_PACKET, socket.SOCK_RAW)
        print('*********************')
        self.sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        self.sock.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
        if sys.platform == 'linux':
            self.sock.setsockopt(socket.SOL_TCP, socket.TCP_KEEPIDLE, 60)
            self.sock.setsockopt(socket.SOL_TCP, socket.TCP_KEEPCNT, 4)
            self.sock.setsockopt(socket.SOL_TCP, socket.TCP_KEEPINTVL, 15)
        self.host = host
        self.port = port
        self.sock.bind((host, port))
        self.sock.listen(max_clients)
        self.sock_threads = []
        self.counter = 0 # Will be used to give a number to each thread

    def close(self):
        """ Close the client socket threads and server socket if they exists. """
        print('Closing server socket (host {}, port {})'.format(self.host, self.port))

        for thr in self.sock_threads:
            thr.stop()
            thr.join()

        if self.sock:
            self.sock.close()
            self.sock = None

    def run(self):
        """ Accept an incoming connection.
        Start a new SocketServerThread that will handle the communication. """
        print('Starting socket server (host {}, port {})'.format(self.host, self.port))

        self.__stop = False
        while not self.__stop:
            self.sock.settimeout(1)
            try:
                client_sock, client_addr = self.sock.accept()
            except socket.timeout:
                client_sock = None

            if client_sock:
                client_thr = SocketServerThread(client_sock, client_addr, self.counter)
                self.counter += 1
                self.sock_threads.append(client_thr)
                client_thr.start()
        self.close()

    def stop(self):
        self.__stop = True


'''
SocketServerThread contains:

    Constructor: simply set variables used to keep track of the socket address and port, and the number assigned to this thread.
    run() : Keep accepting incoming data until the thread is terminated, or the client has disconnected (detected by receiving an empty message when select  indicates that rdy_read  is greater than 0). On incoming messages we just print the message out.
    stop() : Stop the execution of the run()  loop.
    close() : Properly close the socket.

'''

class SocketServerThread(Thread):
    def __init__(self, client_sock, client_addr, number):
        """ Initialize the Thread with a client socket and address """
        Thread.__init__(self)
        self.client_sock = client_sock
        self.client_addr = client_addr
        self.number = number

    def run(self):
        print("[Thr {}] SocketServerThread starting with client {}".format(self.number, self.client_addr))
        self.__stop = False
        while not self.__stop:
            if self.client_sock:
                # Check if the client is still connected and if data is available:
                try:
                    rdy_read, rdy_write, sock_err = select.select([self.client_sock,], [self.client_sock,], [], 5)
                except select.error as err:
                    print('[Thr {}] Select() failed on socket with {}'.format(self.number,self.client_addr))
                    self.stop()
                    return

                if len(rdy_read) > 0:
                    read_data = self.client_sock.recv(255)
                    #print(self.client_sock)
                    #print(rdy_read)
                    #print(read_data)
                    #print(len(read_data))
                    # Check if socket has been closed
                    if len(read_data) == 0:
                        #print('ciao')
                        print('[Thr {}] {} porca putrella, non arrivano dati.'.format(self.number, self.client_addr))
                        #print('[Thr {}] {} closed the socket.'.format(self.number, self.client_addr))
                        self.stop()
                    else:
                        # Strip newlines just for output clarity
                        ip_addr=self.client_addr
                        #print(ip_addr[0])
                        try:
                            dati=read_data.rstrip().split()
                            gnss_data=dati[0].decode('utf-8')
                            gnss_ora=dati[1].decode('utf-8')
                            gnss_data_ora="{0} {1}".format(gnss_data, gnss_ora)
                            # print(type(dati[0].decode('utf-8')))
                            lat=float(dati[2])
                            lon=float(dati[3])
                            quota=float(dati[4])
                            quality=int(dati[5])
                            nsat=int(dati[6])
                            sdn=float(dati[7])
                            sde=float(dati[8])
                            sdu=float(dati[9])
                            sdne=float(dati[10])
                            sdue=float(dati[11])
                            sdun=float(dati[12])
                            age=float(dati[13])
                            ratio=float(dati[14])
                            #print('[Thr {}] Received {}'.format(self.number, read_data.rstrip()))
                            query='INSERT INTO demo_rfi.posizioni(thread, ip, data, lat, lon, ' \
                                'quota, quality, nsat, sde, sdn, sdu, sdne, sdue, sdun, age, ratio)' \
                                ' VALUES ({}, \'{}\', \'{}\', {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {},' \
                                ' {});'.format(self.number, ip_addr[0], gnss_data_ora, lat, lon, quota,
                                                quality, nsat, sde, sdn, sdu, sdne, sdue, sdun, age, ratio);
                            #print(query)
                            curr.execute(query)
                            print('{} - Data saved on PostgreSQL'.format(ip_addr[0]))
                        except:
                            print('*******************************')
                            print('String not complete from IP {}'.format(ip_addr[0]))
                            print('*******************************')





            else:
                print("[Thr {}] No client is connected, SocketServer can't receive data".format(self.number))
                self.stop()
        self.close()

    def stop(self):
        self.__stop = True

    def close(self):
        """ Close connection with the client socket. """
        if self.client_sock:
            print('[Thr {}] Closing connection with {}'.format(self.number, self.client_addr))
            self.client_sock.close()



def main():
    # Start socket server, stop it after a given duration
    duration = 20 * 60
    server = SocketServer()
    #while True:
    server.start()
    #time.sleep(duration)
    #server.stop()
    #server.join()
    #print('End.')


if __name__ == "__main__":
    main()