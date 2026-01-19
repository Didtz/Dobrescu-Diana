# Docker Configuration Guide

## Getting Started with Docker

### Prerequisites
- Docker Desktop installed (https://www.docker.com/products/docker-desktop)
- Docker daemon running

### Build and Run

1. **Build the Docker image:**
   ```bash
   docker build -t pharmacy-app .
   ```

2. **Run with Docker directly:**
   ```bash
   docker run -d -p 80:80 --name pharmacy-container pharmacy-app
   ```

3. **Run with Docker Compose (recommended):**
   ```bash
   docker-compose up -d
   ```

### Useful Commands

- **View running containers:**
  ```bash
  docker ps
  ```

- **View all containers (including stopped):**
  ```bash
  docker ps -a
  ```

- **Stop the application:**
  ```bash
  docker-compose down
  ```
  or
  ```bash
  docker stop pharmacy-container
  ```

- **View logs:**
  ```bash
  docker-compose logs -f
  ```
  or
  ```bash
  docker logs -f pharmacy-container
  ```

- **Access the application:**
  Open your browser and go to: http://localhost

### Project Structure

- `Dockerfile` - Containerization configuration
- `docker-compose.yml` - Multi-container orchestration
- `.dockerignore` - Files to exclude from Docker build

### Notes

- The application runs on port 80 (HTTP)
- Files are mounted as volumes for live development
- The container automatically restarts unless stopped
