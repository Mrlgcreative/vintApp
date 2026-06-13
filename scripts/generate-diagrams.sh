#!/bin/bash
cd "$(dirname "$0")/docs/diagrams"
for f in *.puml; do
    echo "Generation de ${f%.*}.png..."
    java -jar /tmp/plantuml.jar -tpng "$f"
done
echo "Termine."
