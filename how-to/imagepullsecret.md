<h1>How to pull docker private images in K8s</h1>

**1.set variables:**
```
DOCKER_REGISTRY_SERVER=docker.io
DOCKER_USER=Type your dockerhub username, same as when you `docker login`
DOCKER_EMAIL=Type your dockerhub email, same as when you `docker login`
DOCKER_PASSWORD=Type your dockerhub pw, same as when you `docker login`
```

**2. create secret**
```
kubectl create secret docker-registry myregistrykey \
  --docker-server=$DOCKER_REGISTRY_SERVER \
  --docker-username=$DOCKER_USER \
  --docker-password=$DOCKER_PASSWORD \
  --docker-email=$DOCKER_EMAIL
  ```
  
**3. define secret in manifest.**
```
apiVersion: v1
kind: Pod
metadata:
  name: whatever
spec:
  containers:
    - name: whatever
      image: DOCKER_USER/PRIVATE_REPO_NAME:latest
      imagePullPolicy: Always
      command: [ "echo", "SUCCESS" ]
  imagePullSecrets:
    - name: myregistrykey
 
 ```
    
