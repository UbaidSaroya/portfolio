pipeline {
    agent any

    environment {
        IMAGE_NAME = "ubaid-portfolio"
        REMOTE_USER = "ec2-user"
        REMOTE_HOST = "15.207.113.34"
        SSH_CREDENTIALS = "ec2-ssh"
    }

    stages {
        stage('Checkout Code') {
            steps {
                git 'https://github.com/UbaidSaroya/portfolio-responsive-complete-main.git'
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    sh "docker build -t $IMAGE_NAME ."
                }
            }
        }

        stage('Test Docker Container') {
            steps {
                script {
                    sh "docker run -d -p 8080:80 --name test-container $IMAGE_NAME"
                    sh "sleep 5"
                    sh "docker ps"
                    sh "docker stop test-container"
                    sh "docker rm test-container"
                }
            }
        }

        stage('Deploy to EC2') {
            steps {
                sshagent (credentials: [SSH_CREDENTIALS]) {
                    // Stop and remove old container if exists
                    sh "ssh -o StrictHostKeyChecking=no $REMOTE_USER@$REMOTE_HOST 'docker stop portfolio || true && docker rm portfolio || true'"
                    // Remove old image if exists
                    sh "ssh $REMOTE_USER@$REMOTE_HOST 'docker rmi $IMAGE_NAME || true'"
                    // Save and copy new image
                    sh "docker save $IMAGE_NAME | bzip2 | ssh $REMOTE_USER@$REMOTE_HOST 'bunzip2 | docker load'"
                    // Run new container
                    sh "ssh $REMOTE_USER@$REMOTE_HOST 'docker run -d -p 80:80 --name portfolio $IMAGE_NAME'"
                }
            }
        }
    }
}
