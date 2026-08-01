# ---- Static web server ----
FROM nginx:1.27-alpine
WORKDIR /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY index.html ./
COPY css ./css
COPY js ./js
COPY images ./images
COPY scroll-layouts ./scroll-layouts
COPY robots.txt ./
COPY llms.txt ./
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
