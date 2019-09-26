#!/usr/bin/env python
# -*- coding: utf-8 -*-

# Roberto Marzocchi (Gter srl) copyleft 2019

from subprocess import Popen
import os, sys
import datetime

x = datetime.datetime.now()
#print(x)



#recupero il percorso al file
path=os.path.realpath(__file__).replace('forever.py','')
#print(path)
logfile="{0}crash.log".format(path)
f = open(logfile, "a")
#print(logfile)

#quit()

f.write("\n{} - Partito lo script forever.py".format(x))
f.close
filename = sys.argv[1]
while True:
    # ricalcolo le ore
    x = datetime.datetime.now()
    f = open(logfile, "a")
    f.write("\n{} - Ripartito lo script {}".format(x,filename))
    f.close
    print("\nStarting " + filename)
    p = Popen("python3 " + filename, shell=True)
    p.wait()
