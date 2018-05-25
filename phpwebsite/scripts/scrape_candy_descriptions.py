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

#(22117, u'https://www.candystore.com/hersheys-miniature-chocolate-bars-bulk-case/')

def get_description(url):
    try:
        r = requests.get(url)
    except (urllib2.HTTPError):
        #print(e.code)
        pass
    except (urllib2.URLError):
        #print(e.args)
        pass
    else:
        soup = BeautifulSoup(r.text,'html.parser')
        desc = soup.findAll("div", { "class" : "desc" })
        for d in desc:
            if type(d) == bs4.element.Tag:
                if d.getText():
                    return d.getText()
        


data = json.load(open('../json_files/candy_search.json'))
count = 0
# f = open('../json_files/candy_search_desc','w')
# f.write("[\n")
# f.close() 
newitems = []
for item in data:
    #if(count > 22117):
    print(item['href'])
    item['desc'] = get_description(item['href'])
    sleep(abs(random()-.4))
    print(count , item['href'])
    # f = open('../json_files/candy_search_desc.json','a')
    # f.write(json.dumps(item))
    # f.close() 
    #count += 1
    newitems.append(item)
f = open('../json_files/candy_search_desc.json','w')
f.write(json.dumps(newitems))
f.close()
