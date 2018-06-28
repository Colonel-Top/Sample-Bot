#!/usr/bin/python
#-*-coding: utf-8 -*-

import json
import sys

if len(sys.argv) < 2:
  sys.exit(0)

message = sys.argv[1]
result = "hello"
print ("%s" % result)
