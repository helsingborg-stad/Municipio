Helsingborg Stads intranät
========

**Note** This project is a work in progress. Feel free to use it but be aware of eventually upcoming breaking changes.

This is the official repository of the intranet of Helsingborg Stad.

## Getting started
To be updated soon. For now, please get in touch with us for instructions.

## Documentation
Please refer to respective theme and/or plugin for deeper documentation.

## Licence
Please note that licences for this project are specified for each theme and/or plugin. Please refer to respective theme and/or plugin in search for licences.

Elasticsearch
-------------

We use Elasticsearch as our prefered search engine. Here's a few things you might need:

#### List indexes
<sup>*Note:* If the response is ```health status index pri rep docs.count docs.deleted store.size pri.store.size``` you have no indexes.</sup>
```
$ curl 'http://localhost:9200/_cat/indices?v'
```

#### Delete all indexes
<sub>*Note:* For this to work, you need to make sure that the setting ```action.destructive_requires_name``` is set to false. Settings it to true will disabel the option to delete all indexes at once.</sub>
```
$ curl -XDELETE 'http://localhost:9200/_all'
```

#### Delete specific index
```
$ curl -XDELETE 'http://localhost:9200/<index_name>'
```
