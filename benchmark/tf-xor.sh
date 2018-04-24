#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

docker run -it -p 8888:8888 -v ${DIR}/tf-xor.py:/notebooks/tf-xor.py tensorflow/tensorflow python /notebooks/tf-xor.py

