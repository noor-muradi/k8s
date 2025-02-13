**How to Deploy SonaType Nexus Repository Server on EKS**
1. generate secret key:

```
openssl rand -base64 32
```
Copy the base64 encoded secret key and create the Nexus encryption key as below:

```
{
  "active": "example",
  "keys": [
    {
      "id": "example",
      "key": "<base64_encryptionkey>"
    }
  ]
}

```

2. Create a Kubernetes secret to store the the key:

```
kubectl create secret generic nexus-encryption-key --from-file=key.json
```

3. Deploy and create Secret, Configmap, PersistentVolume, Persistent Volume Claim, Deployment, and Ingress objects.

```
kubectl create -f .
```

4. Access the Nexus GUI and to create first repo i.e Docker repo:

      a. After login, click on gear icon > `Repository` > `Create Repository`:
         Select `docker hosted` > Enter port HTTP `5000`. > Click on `Create Repository`.

      b. On your system authenticate docker to Nexus repo, run below command to login:
        ```
        docker login nexus.example.com:5000
        ```
      c. Push image to Nexus created repo:

        ```
        docker push nexus.example.com:5000/repository/example/image:tag
        ```    
