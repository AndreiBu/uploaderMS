## Build docker and project

```bash
git clone git@bitbucket.org:ilyavovnenko/diversland.git
docker-compose up --build -d
```

after starting docker to run composer and artisan migrate 
```bash
docker exec uploader /preset.sh
```

run bush in docker 
```bash
docker exec -it uploader bash