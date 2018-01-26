FROM mongo:3.7.1-jessie

RUN apt-get update && apt-get install -y build-essential git

RUN git clone https://github.com/TylerBrock/mongo-hacker \
    && cd mongo-hacker \
    && make install \
    && cd .. \
    && rm -rdf mongo-hacker
