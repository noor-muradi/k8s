<h1> How to install nginx ingress controller on K8s</h1>

1. Get Repo Info
```
helm repo add ingress-nginx https://kubernetes.github.io/ingress-nginx
helm repo update
```
2. Install Chart
```
helm install [RELEASE_NAME] ingress-nginx/ingress-nginx -f values.yaml
```
The command deploys ingress-nginx on the Kubernetes cluster in the default configuration.

3. Uninstall Chart
```
helm uninstall [RELEASE_NAME]
```
This removes all the Kubernetes components associated with the chart and deletes the release.

4. Upgrading Chart
```
helm upgrade [RELEASE_NAME] [CHART] --install
```
