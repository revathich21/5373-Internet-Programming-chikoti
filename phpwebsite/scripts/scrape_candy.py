# Primary file
import requests
from random import randint, shuffle
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

def get_candy(key,page=None):
    products = []
    try:
        if page:
            #url = "https://www.candystore.com/%s/p=%d" % (key,page)
            url = "https://www.candystore.com/catalogsearch/result/index/?p=%d&q=%s" % (page,key)
        else:
            #url = "https://www.candystore.com/%s/" % (key) 
            url = "https://www.candystore.com/catalogsearch/result/?q=%s" % (key) 

            
        r = requests.get(url)
    except (urllib2.HTTPError):
        #print(e.code)
        pass
    except (urllib2.URLError):
        #print(e.args)
        pass
    else:
        soup = BeautifulSoup(r.text,'html.parser')
        prods = soup.findAll("li", { "class" : "item" })
        
        for prod in prods:
            pdict = {'type':key}
            for p in prod:
                if type(p) == bs4.element.NavigableString:
                    pass
                if type(p) == bs4.element.Tag:
                    if p['class'][0] == 'image-wrap':
                        print(p.a['title'])
                        pdict['title'] = p.a['title']
                        pdict['href'] = p.a['href']
                        print(p.img['src'])
                        pdict['img'] = p.img['src']
                    if p['class'][0] == 'product-info':
                        for d in p:
                            if type(d) != bs4.element.NavigableString:
                                myspan = d.find("span", { "class" : "price" })
                                if myspan:
                                    print(myspan.text)
                                    pdict['price'] = myspan.text
            products.append(pdict)
    return products

types = ['individually-wrapped',
'unwrapped-loose',
'gummy',
'old-fashioned',
'chocolate',
'salt-water-taffy',
'lollipops-suckers',
'hard-candy',
'bagged-candy',
'candy-bars',
'caramel',
'cinnamon-red-hot',
'coated',
'foil-wrapped',
'gum-bubblegum',
'jawbreakers',
'jelly-beans',
'jewelry-edible',
'kosher',
'licorice',
'liquid-gel',
'lollipops-suckers',
'marshmallow',
'mini-sized',
'mints',
'novelty',
'nuts',
'powder',
'retro',
'rock-candy',
'scoops-containers-displays',
'soft',
'sour',
'sports-candy',
'sugar-free',
'theater-king-size',
'toppings-sprinkles',
'toys',
'wax',
'candy-types/individually-wrapped',
'candy-types/foil-wrapped',
'fundraising',
'vending-machines',
'colors/assorted-colored-candy']

types2 = [
'brands/pez/',
'brands/jolly-rancher/',
'brands/charms/',
'brands/ghirardelli/',
'brands/pop-rocks/',
'brands/hershey/',
'brands/mms/',
'brands/sixlets/',
'brands/jelly-belly/',
'candy-types/lollipops-suckers/whirly-pops/',
'brands/tootsie-roll/',
'brands/haribo/',
'brands/skittles/',
'brands/taffy-town/',
'brands/alberts-candy/',
'brands/american-licorice/',
'brands/ashers/',
'brands/atkinsons/',
'brands/ausome/',
'brands/bee-international/',
'brands/blow-pops/',
'bogdons-stick-candy/',
'brands/bonomo-turkish-taffy/',
'brands/brachs/',
'brands/brown-haley-roca/'
]

search_words = [
'pez',
'jolly rancher',
'charms',
'ghirardelli',
'pop rocks',
'hershey',
'mms',
'sixlets',
'jelly belly',
'whirly pops',
'tootsie roll',
'haribo',
'skittles',
'taffy',
'alberts',
'licorice',
'ashers',
'atkinsons',
'ausome',
'bee international',
'blow pops',
'stick candy',
'turkish taffy',
'brachs',
'brown haley roca',
'toys',
'banana',
'chocolate',
'sticky',
'hard',
'sports'
]

all_products = []
for t in search_words:
    print(t)
    all_products.extend(get_candy(t))
    sleep(.8)
    print(len(all_products))
    for i in range(2,4):
        print(t+str(i))
        all_products.extend(get_candy(t,i))
        sleep(.8)
        print(len(all_products))
f = open('../json_files/candy_search.json','a')
f.write(json.dumps(all_products))
f.close()


