<h1> Enable basic auth in Nginx Ingress Controller</h1>

1. create htpasswd file:

a. install htpasswd:

```
sudo apt install apache2-utils -y
```

b. create the username and password:

note: the file name should be "auth", otherwise you will get 503 error.

```
htpasswd -c -b auth noor 1234
```
2. create secret using the "auth" file:

```
kubectl create secret generic basic-auth --from-file=auth
```
3. define basic auth in ingress manfiest:

```
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: my-ingress
  namespace: istio-system
  annotations:
    kubernetes.io/ingress.class: "nginx"
    nginx.ingress.kubernetes.io/auth-type: "basic"
    nginx.ingress.kubernetes.io/auth-secret: "basic-auth"
    nginx.ingress.kubernetes.io/auth-realm: 'Authentication Required'
spec:
  rules:
    - host: example.com
      http:
        paths:
        - path: /
          pathType: Prefix
          backend:
            service:
              name: my-service
              port:
                number: 80

```
