#!/bin/bash

VERSION=1.4.2

if [ ! -d dist ]
then
	mkdir dist
fi

rm -f dist/IM-web-${VERSION}.tar.gz
tar --exclude=create_tar.sh --exclude=docker --exclude=doc --exclude=dist --exclude=.git  -czf dist/IM-web-${VERSION}.tar.gz *
