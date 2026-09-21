export async function fetcher(url, data = {}, method = "POST") {
  const config = {
    method,
    headers: {},
  };

  if (data instanceof FormData) {
    config.body = data;
  } else {
    config.headers["Content-Type"] = "application/json";
    config.body = JSON.stringify(data);
  }

  const res = await fetch(url, config);
  const json = await res.json();

  if (!res.ok || json.success === false) {
    throw new Error(json.message || "요청 실패");
  }

  return json;
}
