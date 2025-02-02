<h1> Deploy Rancher on K8s cluster</h1>

1. Create namespace

```
kubectl create namespace cattle-system
```
2. Add Helm repo of the Rancher:

```
helm repo add rancher-stable https://releases.rancher.com/server-charts/stable
```

3. Deploy the Rancher using helm:

```
helm install rancher rancher-stable/rancher \
  --namespace cattle-system \
  --set hostname=rancher.example.com \
  --set bootstrapPassword=mypassword \
  --set ingress.tls.source=letsEncrypt \
  --set letsEncrypt.email=noor@example.com \
  --set letsEncrypt.ingress.class=nginx \
  --set ingress.ingressClassName=nginx
```
