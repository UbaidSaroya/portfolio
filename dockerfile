# Use official Nginx image
FROM nginx:alpine

# Remove default site
RUN rm -rf /usr/share/nginx/html/*

# Copy project files into Nginx directory
COPY . /usr/share/nginx/html

# Expose port 80 inside the container
EXPOSE 80
