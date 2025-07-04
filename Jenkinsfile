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
                    def container = docker.image('ubaid-portfolio').run('-p 8081:80')
                    try {
                        sh 'sleep 5'
                        sh 'curl -f http://localhost:8081'
                    } finally {
                        container.stop()
                    }
                }
            }
        }

        stage('Deploy to EC2') {
            steps {
                sshagent (credentials: ['ec2-ssh']) {
                    sh '''
                        # 1) Clean old container and image on EC2
                        ssh -o StrictHostKeyChecking=no ec2-user@15.207.113.34 '
                            docker stop portfolio || true
                            docker rm portfolio || true
                            docker rmi ubaid-portfolio || true
                        '

                        # 2) Remove old tar files locally
                        rm -f ubaid-portfolio.tar ubaid-portfolio.tar.bz2

                        # 3) SAVE IMAGE - IMPORTANT: Image exists here
                        docker save -o ubaid-portfolio.tar ubaid-portfolio

                        # 4) Compress the tar
                        bzip2 ubaid-portfolio.tar

                        # 5) Copy tar.bz2 to EC2
                        scp -o StrictHostKeyChecking=no ubaid-portfolio.tar.bz2 ec2-user@15.207.113.34:/home/ec2-user/

                        # 6) Load image and start container on EC2
                        ssh -o StrictHostKeyChecking=no ec2-user@15.207.113.34 '
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
