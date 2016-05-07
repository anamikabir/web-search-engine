import csv
import networkx as nx
D={}
with open('UrlMap.csv','rb') as f:
	reader=csv.reader(f,delimiter=',')
	for row in reader:
		D[row[0]]=row[1]
G=nx.DiGraph()
G_all=nx.DiGraph()
with open('pagerank.csv','rb') as ff:
	reader=csv.reader(ff,delimiter=',')
	for row in reader:
		mynode = D[row[0]]
		G.add_node(mynode)
		startingnodeURL=row[0]
		G_all.add_node(startingnodeURL)
		for col in row:
			newnode=D.get(col,"empty")
			G_all.add_node(col)
			G_all.add_edge(startingnodeURL,col)
			if(newnode!="empty"):
				G.add_node(newnode)
				G.add_edge(mynode,newnode)
pr=nx.pagerank(G,alpha=0.85)
with open("external_PageRankData.txt", "a+") as myfile:
	for k,v in pr.items():
		myStr="/home/anamika/shared/"+k+'='+str(v)+"\n"
		myfile.write(myStr)
pr_all=nx.pagerank(G_all,alpha=0.85)
with open("external_PageRankData_all.txt", "a+") as myfile1:
        for ke,va in pr_all.items():
                myString=ke+'='+str(va)+"\n"
                myfile1.write(myString)

