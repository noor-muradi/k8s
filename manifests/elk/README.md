<h1> Deploy ECK in your Kubernetes cluster </h1>

1. Install custom resource definitions:

```
kubectl create -f crd.yaml
```
2. Install the operator with its RBAC rules:

```
kubectl create -f operator.yaml
```
The ECK operator runs by default in the elastic-system namespace. It is recommended that you choose a dedicated namespace for your workloads, 
rather than using the elastic-system or the default namespace.

3. Monitor the operator logs:

```
kubectl -n elastic-system logs -f statefulset.apps/elastic-operator
```
4. Deploy ECK on K8s cluster:

```
kubectl apply -f elastic.yaml
kubectl get elasticsearch
```
5. Get the ES default credential:
```
kubectl get secret monitoring-es-elastic-user -o go-template='{{.data.elastic | base64decode}}'
```
6. Deploy Kibana on K8s cluster:

```
kubectl apply -f kibana.yaml
kubectl get kibana
```
7. Create Index lifecycle Policy and template via Dev-Tool in Kibana:

  A. Create index lifecycle policy:  
  
  ```
  PUT _ilm/policy/example-alb-ilm-policy
  {
    "policy": {
      "phases": {
        "hot": {
          "actions": {
            "rollover": {
              "max_age": "15d",
              "max_primary_shard_size": "15gb"
            }
          }
        },
        "warm": {
          "min_age": "15d",
          "actions": {
            "allocate": {
              "number_of_replicas": 0
            }
          }
        },
        "delete": {
          "min_age": "30d",
          "actions": {
            "delete": {}
          }
        }
      }
    }
  }
  
  ```
  B: Create the empty indice:
  
  ```
  PUT example-alb-0000001
  {
    "aliases": {
      "example-alb-alias": {
        "is_write_index": true
      }
    }
  }
  ```
  C: create index template with required settings and mappings
  ```
  PUT _index_template/example-alb
  {
    "index_patterns": [
      "example-alb-*"
    ],
    "template": {
      "settings": {
        "index": {
          "lifecycle": {
            "name": "example-alb-ilm-policy",
            "rollover_alias": "example-alb-alias"
          },
          "refresh_interval": "60s",
          "number_of_shards": "2",
          "number_of_replicas": "1"
        }
      },
      "mappings": {
        "properties": {
          "geoip": {
            "properties": {
              "location": {
                "type": "geo_point"
              }
            }
          }
        }
      }
    }
  }
  
  ```
   
8. Deploy Logstash on K8s cluster:

```
kubectl apply -f logstash.yaml
kubectl get logstash
```
