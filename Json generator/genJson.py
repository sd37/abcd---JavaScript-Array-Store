import sys
import jsonpickle.pickler
import pickle
from pickle import Pickler
import simplejson

sys.path.append('/opt/scidb/12.7/lib')

if(len(sys.argv) < 2):
    print "Error : Less Number of Arguments"
    print "usage : genJson [dimension]"
    sys.exit()


#print sys.argv

import scidbapi as scidb
#import scidb.swig.QueryResult

def print_dataitem(dtypeStr, dataitem, innerAttrList):
    value = scidb.getTypedValue(dataitem, dtypeStr)
#    print "Data: %s" % value
    innerAttrList.append(value)

db = scidb.connect("localhost", 1239)
result = db.executeQuery("SELECT * from " + sys.argv[2] , 'aql')

    #help(scidb.swig.QueryResult)

desc = result.array.getArrayDesc()
dims = desc.getDimensions()
attrs = desc.getAttributes()

dimension = dims.size()
params    = attrs.size()

#print("Dimensions")
#for i in range (dims.size()):
#    print "Dimension[%d] = %d:%d,%d,%d\n" % (i, dims[i].getStartMin(), dims[i].getEndMax(),
#                                                    dims[i].getChunkInterval(), dims[i].getChunkOverlap())

#print("Attributes")
#for i in range (attrs.size()):    
#    print "attribute %d %s %s" % (attrs[i].getId(), attrs[i].getName(), attrs[i].getType())

#print "Array iterators."
iters = []
for i in range (attrs.size()):
    attrid = attrs[i].getId()
    iters.append(result.array.getConstIterator(attrid))
#    print "attribute attrid = %d loaded" % (attrid)

nc = 1;
attrList = []
while not iters[0].end():

    for i in range (attrs.size()):
        if (attrs[i].getName() == "EmptyTag"):
            continue
        innerAttrList = []
#        print "Getting iterator for attribute %d, chunk %d.\n" % (i, nc)
        currentchunk = iters[i].getChunk()
        chunkiter = currentchunk.getConstIterator((scidb.swig.ConstChunkIterator.IGNORE_OVERLAPS)|(scidb.swig.ConstChunkIterator.IGNORE_EMPTY_CELLS))

        printed=False
        while not chunkiter.end():
            if not chunkiter.isEmpty():
                dataitem = chunkiter.getItem()
                print_dataitem(attrs[i].getType(), dataitem, innerAttrList)
                printed=True

            chunkiter.increment_to_next()
        if printed:
            pass       # add an extra newline to separate the data from the next query
        attrList.append(innerAttrList)

    nc += 1;
    for i in range(attrs.size()):
        iters[i].increment_to_next();
#j = 0
#while j < len(attrList[0]):    
#   for i in range (len(attrList)):

#      attrList[i][j]
#   j = j + 1    

f = open("/var/www/bin/" +"dim"+sys.argv[1]  + ".json","w")
print "dim" + sys.argv[1]  + ".json"

argvDim = int(sys.argv[1])
if (argvDim < dimension):
    simplejson.dump(attrList[argvDim],f)
    f.write('\n')
    f.close()
    sys.exit()

dimensionList = []

for i in range(len(attrList)):
    dimensionList.append(attrList[i])

zipDimList = zip(*dimensionList)

i = 0
j = 0
l = []
jsonRep = []

while i < len(zipDimList):
    if(j == params):
        jsonRep.append(l)
        j = 0
        l = []
        continue
    l.append(zipDimList[i])
    i = i + 1
    j = j + 1

if (l):
    jsonRep.append(l)

#print zipDimList
#print jsonRep

l = []

#write the json representation to file..
simplejson.dump(jsonRep,f)
f.write('\n')
f.close()
db.completeQuery(result.queryID)
