from HTMLParser import HTMLParser
import glob
import os
class LinksParser(HTMLParser):
	def handle_data(self, data):
		with open("big.txt", "a+") as myfile:
			myfile.write(data.decode("utf8"))
path='/home/anamika/shared'
for filename in glob.glob(os.path.join(path, '*.html')):
	try:
		parser = LinksParser()
		parser.feed(open(filename).read())
	except:
		print 'error'
