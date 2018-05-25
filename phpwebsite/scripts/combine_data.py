# Primary file
import requests
from random import randint, shuffle, random
from time import sleep
import datetime,time 
import sys
#sys.path.append("/usr/local/lib/python2.7/site-packages/bs4")
import bs4
from bs4 import BeautifulSoup
import json
import pprint as pp
import glob
import collections
from os import chdir
import urllib2

files = ['candy_full_w_desc2','candy_full_w_desc3','candy_full_w_desc','candy_search_desc']#

combined = {}
images = {}

id = 0

for file in files:
    name = "../json_files/"+file+".json"
    print(name)
    data = json.load(open(name))
    for item in data:
        if not item['title'] in combined:
            item['id'] = id
            combined[item['title']] = item
            images[id] = item['img']
            id = id + 1

print(len(combined.keys()))

# f1 = open('../json_files/candy_combined.json','w')
# f1.write(json.dumps(combined,indent=4, separators=(',', ': ')))
# f2 = open('../json_files/candy_combined_images.json','w')
# f2.write(json.dumps(images,indent=4, separators=(',', ': ')))

f3 = open('../json_files/candy_combined_wget.sh','w')
for k,v in images.items():
    ext = v[-3:]
    small = v
    large = v.replace("small_image", "image")
    large = large.replace("200x/", "")
    f3.write("wget "+small+" -O /var/www/html/cimages/"+str(k)+"_small."+ext+"\nsleep .5\n")
    f3.write("wget "+large+" -O /var/www/html/cimages/"+str(k)+"_large."+ext+"\nsleep .5\n")