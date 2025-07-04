pipeline {
    agent any

    stages {
        stage('Checkout Code') {
            steps {
                git branch: 'main',
                    url: 'https://github.com/UbaidSaroya/portfolio.git'
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    docker.build('ubaid-portfolio')
                }
            }
        }

        stage('Test Docker Container') {
            steps {
                script {
                    docker.image('ubaid-portfolio').withRun('-p 8081:80') { c ->
                        sh 'sleep 5'
                        sh 'curl -f http://localhost:8081'
                    }
                }
            }
        }

        stage('Deploy to EC2') {
            steps {
                sshagent (credentials: ['ec2-ssh']) {
                    sh '''
                    # Clean any old files
                    rm -f ubaid-portfolio.tar.bz2

                    # Save the Docker image to tar.bz2
                    docker save ubaid-portfolio | bzip2 > ubaid-portfolio.tar.bz2

                    # Copy the tar.bz2 file to EC2
                    scp -o StrictHostKeyChecking=no ubaid-portfolio.tar.bz2 ec2-user@15.207.113.34:/home/ec2-user/

                    # Load the image and restart container on EC2
                    ssh -o StrictHostKeyChecking=no ec2-user@15.207.113.34 '
                        docker stop portfolio || true
                        docker rm portfolio || true
                        bunzip2 -f ubaid-portfolio.tar.bz2
                        docker load -i ubaid-portfolio.tar
                        rm -f ubaid-portfolio.tar
                        docker run -d -p 80:80 --name portfolio ubaid-portfolio
                    '
                    '''
                }
            }
        }
    }
}
