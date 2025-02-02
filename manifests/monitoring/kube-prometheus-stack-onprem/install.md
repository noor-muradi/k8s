1.Add helm repo
```
helm repo add prometheus  https://prometheus-community.github.io/helm-charts
helm repo update
```
2. install Prometheus stack using modified values
```
helm install monitoring prometheus/kube-prometheus-stack -n monitoring -f values.yaml
```
