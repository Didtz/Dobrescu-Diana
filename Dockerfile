# Create a lightweight container using Node.js
FROM node:18-alpine

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Install a simple HTTP server globally
RUN npm install -g http-server

# Expose port 8000
EXPOSE 8000

# Start HTTP server
CMD ["http-server", "-p", "8000", "-c-1"]
