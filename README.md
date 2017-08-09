# minmods

## Introduction
Create minimal mods from book/vol directory names to allow quick ingests and later editing of MODS.

Does not overwrite existing metadata files.

## Additional

### minimgmods

Create minimal mods for images in a directory

Does not overwrite existing files.

## Installation

Copy into /usr/local/bin and change name to minmods ( or in the image script case, minimgmods).
Change permissions to 755.

## Usage

### minmods

For a book type directory, where the main directory is the collection name/namespace, with the items as separate directories beneath it:

* Use the name of each item to make the title and identifier and file name of a small MODS record to be placed the same directory as the item directory.
* This can then be used with [bookprep](https://github.com/utkdigitalinitiatives/bookprep) to prepare for an islandora book batch ingest.

### minimgmods

For the minimgmods script, a directory has a number of image files and this makes MODS records to match the image file names by using the base file name for the title, identifier and file name of the MODS.

### fixlabel

For the book type objects, after the mods metadata for the main object has been edited, the pages do not reflect the main object's title.  This script looks at all the main objects, saves the titles and admindb identifiers, then  looks at each page, matches it up with an admindb number, does a replace of the admindb number in the titleInfo.title with the main title.

1. export using islandora_datastream_exporter all mods for collection
2. run fixlabel on the directory of exports
3. copy the xxxxxx.done files to another directory
4. using rename
```
rename xml.done xml *.done
```
5. using islandora_datastream_replace, replace these into the collection
6. using fedora_delete_datastreams delete previous version of metadata
