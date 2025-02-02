To manually delete index's old documents run below command in Dev-Tools:

```
POST /index_name/_delete_by_query
{
  "query": {
    "range": {
      "@timestamp": {
        "lt": "now-30d/d"
      }
    }
  },
  "timeout": "5m"  // Set the timeout to 5 minutes (adjust as needed)
}

```
