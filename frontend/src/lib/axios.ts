import axios from "axios";

const axiosInstance = axios.create({
    baseURL: "http://localhost:8000/api",
    withCredentials: true,
    withXSRFToken: true,
});


axiosInstance.interceptors.request.use(
    (config) => {
      const token = localStorage.getItem("access_token");
      const excludedRoutes = ["/login", "/register"];

      if (!excludedRoutes.includes(config.url || "")) {
        if (token) {
          config.headers = config.headers || {};
          config.headers["Authorization"] = `Bearer ${token}`;
        }
      }
  
      return config;
    },
    (error) => {
      return Promise.reject(error);
    }
  );

export default axiosInstance;