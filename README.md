## Build docker and project

```bash
git clone git@github.com:AndreiBu/uploaderMS.git
docker-compose up --build -d
```

after starting docker to run composer and migrate 
```bash
docker exec uploader /preset.sh
```

run bush in docker 
```bash
docker exec -it uploader bash
```

for explore just open url http://localhost:8000

